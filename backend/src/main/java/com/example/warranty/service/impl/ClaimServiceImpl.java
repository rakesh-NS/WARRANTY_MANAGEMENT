package com.example.warranty.service.impl;

import com.example.warranty.dto.ClaimRequestDto;
import com.example.warranty.dto.ClaimResponseDto;
import com.example.warranty.entity.ClaimEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.ClaimRepository;
import com.example.warranty.repository.WarrantyRepository;
import com.example.warranty.service.ClaimService;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.stream.Collectors;

@Service
@Transactional
public class ClaimServiceImpl implements ClaimService {

    private final ClaimRepository claimRepository;
    private final WarrantyRepository warrantyRepository;

    public ClaimServiceImpl(ClaimRepository claimRepository, WarrantyRepository warrantyRepository) {
        this.claimRepository = claimRepository;
        this.warrantyRepository = warrantyRepository;
    }

    @Override
    public ClaimResponseDto submit(ClaimRequestDto request) {
        WarrantyEntity warranty = warrantyRepository.findById(request.getWarrantyId())
                .orElseThrow(() -> new ResourceNotFoundException("Warranty not found with id: " + request.getWarrantyId()));
        ClaimEntity entity = ClaimEntity.builder()
                .warranty(warranty)
                .claimDate(request.getClaimDate())
                .issueDescription(request.getIssueDescription())
                .status("Pending")
                .build();
        return toResponse(claimRepository.save(entity));
    }

    @Override
    public List<ClaimResponseDto> getAll() {
        return claimRepository.findAll().stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public ClaimResponseDto updateStatus(Long id, String status, String adminRemarks) {
        ClaimEntity claim = claimRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Claim not found with id: " + id));
        claim.setStatus(status);
        claim.setAdminRemarks(adminRemarks);
        return toResponse(claimRepository.save(claim));
    }

    private ClaimResponseDto toResponse(ClaimEntity claim) {
        ClaimResponseDto response = new ClaimResponseDto();
        response.setId(claim.getId());
        response.setWarrantyId(claim.getWarranty().getId());
        response.setClaimDate(claim.getClaimDate());
        response.setIssueDescription(claim.getIssueDescription());
        response.setStatus(claim.getStatus());
        response.setAdminRemarks(claim.getAdminRemarks());
        response.setProductId(claim.getWarranty().getProduct().getId());
        return response;
    }
}
