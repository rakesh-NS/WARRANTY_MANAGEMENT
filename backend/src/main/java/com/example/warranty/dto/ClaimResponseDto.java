package com.example.warranty.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class ClaimResponseDto {
    private Long id;
    private Long warrantyId;
    private LocalDate claimDate;
    private String issueCategory;
    private String issueDescription;
    private String status;
    private String adminRemarks;
    private Long productId;
}
