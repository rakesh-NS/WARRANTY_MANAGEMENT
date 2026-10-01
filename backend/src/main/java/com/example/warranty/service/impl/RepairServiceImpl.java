package com.example.warranty.service.impl;

import java.time.LocalDate;
import java.util.List;
import java.util.Locale;
import java.util.stream.Collectors;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.example.warranty.dto.RepairRequestDto;
import com.example.warranty.dto.RepairResponseDto;
import com.example.warranty.entity.ClaimEntity;
import com.example.warranty.entity.RepairEntity;
import com.example.warranty.exception.InvalidClaimStatusException;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.ClaimRepository;
import com.example.warranty.repository.RepairRepository;
import com.example.warranty.service.RepairService;

@Service
@Transactional
public class RepairServiceImpl implements RepairService {

    private final RepairRepository repairRepository;
    private final ClaimRepository claimRepository;

    public RepairServiceImpl(RepairRepository repairRepository, ClaimRepository claimRepository) {
        this.repairRepository = repairRepository;
        this.claimRepository = claimRepository;
    }

    @Override
    public RepairResponseDto create(RepairRequestDto request) {
        ClaimEntity claim = claimRepository.findById(request.getClaimId())
                .orElseThrow(() -> new ResourceNotFoundException("Claim not found with id: " + request.getClaimId()));

        String claimStatus = claim.getStatus() == null ? "" : claim.getStatus().trim().toUpperCase(Locale.ROOT);
        if (!"APPROVED".equals(claimStatus) && !"IN_REPAIR".equals(claimStatus)) {
            throw new InvalidClaimStatusException("Repair can only be created for approved or in-repair claims.");
        }

        RepairEntity repair = new RepairEntity();
        repair.setClaim(claim);
        repair.setProduct(claim.getWarranty().getProduct());
        repair.setUser(claim.getWarranty().getUser());
        repair.setStatus(normalizeStatus(request.getStatus(), "SCHEDULED"));
        repair.setTechnician(request.getTechnician());
        repair.setRemarks(request.getRemarks());
        repair.setCreatedDate(LocalDate.now());

        if ("COMPLETED".equalsIgnoreCase(repair.getStatus())) {
            repair.setCompletedDate(LocalDate.now());
        }

        claim.setStatus("IN_REPAIR");
        claimRepository.save(claim);
        return toResponse(repairRepository.save(repair));
    }

    @Override
    public RepairResponseDto update(Long id, RepairRequestDto request) {
        RepairEntity repair = repairRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Repair not found with id: " + id));

        if (request.getStatus() != null && !request.getStatus().isBlank()) {
            repair.setStatus(normalizeStatus(request.getStatus(), repair.getStatus()));
            if ("COMPLETED".equalsIgnoreCase(repair.getStatus())) {
                repair.setCompletedDate(LocalDate.now());
                repair.getClaim().setStatus("RESOLVED");
            }
        }

        if (request.getTechnician() != null) {
            repair.setTechnician(request.getTechnician());
        }

        if (request.getRemarks() != null) {
            repair.setRemarks(request.getRemarks());
        }

        if (repair.getClaim() != null) {
            claimRepository.save(repair.getClaim());
        }
        return toResponse(repairRepository.save(repair));
    }

    @Override
    public List<RepairResponseDto> getAll() {
        return repairRepository.findAll().stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public List<RepairResponseDto> getByUserId(Long userId) {
        return repairRepository.findByUserId(userId).stream().map(this::toResponse).collect(Collectors.toList());
    }

    private String normalizeStatus(String status, String fallback) {
        return status == null || status.isBlank() ? fallback : status.trim().toUpperCase(Locale.ROOT);
    }

    private RepairResponseDto toResponse(RepairEntity repair) {
        RepairResponseDto response = new RepairResponseDto();
        response.setId(repair.getId());
        response.setClaimId(repair.getClaim() != null ? repair.getClaim().getId() : null);
        response.setProductId(repair.getProduct() != null ? repair.getProduct().getId() : null);
        response.setUserId(repair.getUser() != null ? repair.getUser().getId() : null);
        response.setStatus(repair.getStatus());
        response.setTechnician(repair.getTechnician());
        response.setCreatedDate(repair.getCreatedDate());
        response.setCompletedDate(repair.getCompletedDate());
        response.setRemarks(repair.getRemarks());
        return response;
    }
}
