package com.example.warranty.dto;

import java.time.LocalDate;

import jakarta.validation.constraints.NotBlank;
import lombok.Data;

@Data
public class ClaimRequestDto {
    private Long productId;

    private Long warrantyId;

    private LocalDate claimDate;

    private String issueCategory;

    @NotBlank(message = "Issue description is required")
    private String issueDescription;
}
