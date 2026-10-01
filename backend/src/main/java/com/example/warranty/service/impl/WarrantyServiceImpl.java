package com.example.warranty.service.impl;

import java.time.LocalDate;
import java.util.List;
import java.util.stream.Collectors;

import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.example.warranty.dto.WarrantyRequestDto;
import com.example.warranty.dto.WarrantyResponseDto;
import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;
import com.example.warranty.security.AuthUser;
import com.example.warranty.service.WarrantyService;

@Service
@Transactional
public class WarrantyServiceImpl implements WarrantyService {

    private final WarrantyRepository warrantyRepository;
    private final ProductRepository productRepository;
    private final UserRepository userRepository;

    public WarrantyServiceImpl(WarrantyRepository warrantyRepository,
                               ProductRepository productRepository,
                               UserRepository userRepository) {
        this.warrantyRepository = warrantyRepository;
        this.productRepository = productRepository;
        this.userRepository = userRepository;
    }

    @Override
    public List<WarrantyResponseDto> getAll() {
        UserEntity currentUser = getCurrentUser();
        List<WarrantyEntity> warranties = "ADMIN".equalsIgnoreCase(currentUser.getRole())
                ? warrantyRepository.findAll()
                : warrantyRepository.findAll().stream()
                    .filter(w -> w.getUser() != null && w.getUser().getId().equals(currentUser.getId()))
                    .collect(Collectors.toList());
        return warranties.stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public WarrantyResponseDto save(WarrantyRequestDto request) {
        UserEntity currentUser = getCurrentUser();
        ProductEntity product = productRepository.findById(request.getProductId())
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + request.getProductId()));

        if (!product.getUser().getId().equals(currentUser.getId()) && !"ADMIN".equalsIgnoreCase(currentUser.getRole())) {
            throw new AccessDeniedException("You cannot manage this warranty.");
        }

        WarrantyEntity entity = WarrantyEntity.builder()
                .product(product)
                .user(product.getUser())
                .startDate(request.getStartDate() != null ? request.getStartDate() : product.getPurchaseDate())
                .expiryDate(request.getExpiryDate() != null ? request.getExpiryDate() : product.getPurchaseDate().plusMonths(12))
                .status(request.getStatus() != null ? request.getStatus() : calculateWarrantyStatus(product.getPurchaseDate().plusMonths(12)))
                .warrantyType("Standard Warranty")
                .build();
        return toResponse(warrantyRepository.save(entity));
    }

    @Override
    public WarrantyResponseDto update(Long id, WarrantyRequestDto request) {
        WarrantyEntity warranty = warrantyRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Warranty not found with id: " + id));
        ProductEntity product = productRepository.findById(request.getProductId())
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + request.getProductId()));
        UserEntity user = userRepository.findById(request.getUserId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + request.getUserId()));
        warranty.setProduct(product);
        warranty.setUser(user);
        warranty.setStartDate(request.getStartDate());
        warranty.setExpiryDate(request.getExpiryDate());
        warranty.setStatus(request.getStatus());
        return toResponse(warrantyRepository.save(warranty));
    }

    private String calculateWarrantyStatus(LocalDate expiryDate) {
        if (expiryDate == null) {
            return "ACTIVE";
        }
        if (LocalDate.now().isAfter(expiryDate)) {
            return "EXPIRED";
        }
        return "ACTIVE";
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

    private WarrantyResponseDto toResponse(WarrantyEntity warranty) {
        WarrantyResponseDto response = new WarrantyResponseDto();
        response.setId(warranty.getId());
        response.setProductId(warranty.getProduct().getId());
        response.setUserId(warranty.getUser() != null ? warranty.getUser().getId() : null);
        response.setStartDate(warranty.getStartDate());
        response.setExpiryDate(warranty.getExpiryDate());
        response.setStatus(warranty.getStatus());
        response.setProductName(warranty.getProduct().getProductName());
        return response;
    }
}
