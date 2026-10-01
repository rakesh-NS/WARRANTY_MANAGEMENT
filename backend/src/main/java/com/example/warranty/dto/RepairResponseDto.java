package com.example.warranty.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class RepairResponseDto {
    private Long id;
    private Long claimId;
    private Long productId;
    private Long userId;
    private String status;
    private String technician;
    private LocalDate createdDate;
    private LocalDate completedDate;
    private String remarks;
}
