package com.example.warranty.service.impl;

import java.time.LocalDate;
import java.util.List;
import java.util.Locale;
import java.util.Set;
import java.util.stream.Collectors;

import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.example.warranty.dto.ClaimRequestDto;
import com.example.warranty.dto.ClaimResponseDto;
import com.example.warranty.entity.ClaimEntity;
import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.exception.InvalidClaimStatusException;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.exception.WarrantyExpiredException;
import com.example.warranty.repository.ClaimRepository;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;
import com.example.warranty.security.AuthUser;
import com.example.warranty.service.ClaimService;

@Service
@Transactional
public class ClaimServiceImpl implements ClaimService {

    private final ClaimRepository claimRepository;
    private final WarrantyRepository warrantyRepository;
    private final ProductRepository productRepository;
    private final UserRepository userRepository;

    public ClaimServiceImpl(ClaimRepository claimRepository, WarrantyRepository warrantyRepository,
                           ProductRepository productRepository, UserRepository userRepository) {
        this.claimRepository = claimRepository;
        this.warrantyRepository = warrantyRepository;
        this.productRepository = productRepository;
        this.userRepository = userRepository;
    }

    @Override
    public ClaimResponseDto submit(ClaimRequestDto request) {
        UserEntity currentUser = getCurrentUser();
        ProductEntity product = resolveProduct(request, currentUser);
        WarrantyEntity warranty = product.getWarranty();

        if (warranty == null) {
            throw new ResourceNotFoundException("No warranty is available for this product.");
        }
        if (!product.getUser().getId().equals(currentUser.getId())) {
            throw new AccessDeniedException("You cannot claim for another customer's product.");
        }
        if (warranty.getStatus() == null || "EXPIRED".equalsIgnoreCase(warranty.getStatus())) {
            throw new WarrantyExpiredException("This warranty expired and cannot be used to submit a claim.");
        }
        boolean activeClaimExists = claimRepository.findByWarrantyProductId(product.getId()).stream()
                .anyMatch(claim -> "PENDING".equalsIgnoreCase(claim.getStatus())
                        || "UNDER_REVIEW".equalsIgnoreCase(claim.getStatus())
                        || "APPROVED".equalsIgnoreCase(claim.getStatus())
                        || "REPAIR_IN_PROGRESS".equalsIgnoreCase(claim.getStatus()));
        if (activeClaimExists) {
            throw new IllegalArgumentException("An active claim already exists for this product.");
        }

        ClaimEntity entity = ClaimEntity.builder()
                .warranty(warranty)
                .claimDate(request.getClaimDate() != null ? request.getClaimDate() : LocalDate.now())
                .issueCategory(request.getIssueCategory())
                .issueDescription(request.getIssueDescription())
                .status("PENDING")
                .build();
        return toResponse(claimRepository.save(entity));
    }

    @Override
    public List<ClaimResponseDto> getAll() {
        UserEntity currentUser = getCurrentUser();
        List<ClaimEntity> claims = "ADMIN".equalsIgnoreCase(currentUser.getRole())
                ? claimRepository.findAll()
                : claimRepository.findAll().stream()
                    .filter(claim -> claim.getWarranty() != null
                        && claim.getWarranty().getUser() != null
                        && claim.getWarranty().getUser().getId().equals(currentUser.getId()))
                    .collect(Collectors.toList());
        return claims.stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public ClaimResponseDto updateStatus(Long id, String status, String adminRemarks) {
        ClaimEntity claim = claimRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Claim not found with id: " + id));

        String normalizedStatus = normalizeStatus(status);
        String currentStatus = claim.getStatus() == null ? "" : claim.getStatus().trim().toUpperCase(Locale.ROOT);

        Set<String> validTransitions = switch (currentStatus) {
            case "PENDING" -> Set.of("UNDER_REVIEW", "REJECTED");
            case "UNDER_REVIEW" -> Set.of("APPROVED", "REJECTED");
            case "APPROVED" -> Set.of("REPAIR_IN_PROGRESS", "REJECTED");
            case "REPAIR_IN_PROGRESS" -> Set.of("RESOLVED", "REJECTED");
            case "RESOLVED" -> Set.of("CLOSED");
            case "REJECTED" -> Set.of("CLOSED");
            default -> Set.of();
        };

        if (normalizedStatus.isEmpty() || !validTransitions.contains(normalizedStatus)) {
            throw new InvalidClaimStatusException("Invalid status transition from " + currentStatus + " to " + normalizedStatus);
        }

        claim.setStatus(normalizedStatus);
        claim.setAdminRemarks(adminRemarks);
        return toResponse(claimRepository.save(claim));
    }

    private String normalizeStatus(String status) {
        return status == null ? "" : status.trim().toUpperCase(Locale.ROOT).replace(' ', '_');
    }

    private UserEntity getCurrentUser() {
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        if (authentication == null || !authentication.isAuthenticated() || authentication.getPrincipal() instanceof String) {
            throw new AccessDeniedException("Authentication required.");
        }
        AuthUser authUser = (AuthUser) authentication.getPrincipal();
        return userRepository.findById(authUser.getId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + authUser.getId()));
    }

    private ProductEntity resolveProduct(ClaimRequestDto request, UserEntity currentUser) {
        if (request.getProductId() != null) {
            ProductEntity product = productRepository.findById(request.getProductId())
                    .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + request.getProductId()));
            if (!product.getUser().getId().equals(currentUser.getId()) && !"ADMIN".equalsIgnoreCase(currentUser.getRole())) {
                throw new AccessDeniedException("You cannot claim for another customer's product.");
            }
            return product;
        }
        if (request.getWarrantyId() != null) {
            WarrantyEntity warranty = warrantyRepository.findById(request.getWarrantyId())
                    .orElseThrow(() -> new ResourceNotFoundException("Warranty not found with id: " + request.getWarrantyId()));
            if (!warranty.getUser().getId().equals(currentUser.getId()) && !"ADMIN".equalsIgnoreCase(currentUser.getRole())) {
                throw new AccessDeniedException("You cannot claim for another customer's warranty.");
            }
            return warranty.getProduct();
        }
        throw new IllegalArgumentException("Product or warranty selection is required");
    }

    private ClaimResponseDto toResponse(ClaimEntity claim) {
        ClaimResponseDto response = new ClaimResponseDto();
        response.setId(claim.getId());
        response.setWarrantyId(claim.getWarranty() != null ? claim.getWarranty().getId() : null);
        response.setClaimDate(claim.getClaimDate());
        response.setIssueCategory(claim.getIssueCategory());
        response.setIssueDescription(claim.getIssueDescription());
        response.setStatus(claim.getStatus());
        response.setAdminRemarks(claim.getAdminRemarks());
        response.setProductId(claim.getWarranty() != null && claim.getWarranty().getProduct() != null ? claim.getWarranty().getProduct().getId() : null);
        return response;
    }
}
