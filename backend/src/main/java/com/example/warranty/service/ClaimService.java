package com.example.warranty.service;

import com.example.warranty.dto.ClaimRequestDto;
import com.example.warranty.dto.ClaimResponseDto;

import java.util.List;

public interface ClaimService {
    ClaimResponseDto submit(ClaimRequestDto request);
    List<ClaimResponseDto> getAll();
    ClaimResponseDto updateStatus(Long id, String status, String adminRemarks);
}
