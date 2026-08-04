package com.example.warranty.dto;

import lombok.Data;
import java.time.LocalDate;

@Data
public class ClaimResponseDto {
    private Long id;
    private Long warrantyId;
    private LocalDate claimDate;
    private String issueDescription;
    private String status;
    private String adminRemarks;
    private Long productId;
}
