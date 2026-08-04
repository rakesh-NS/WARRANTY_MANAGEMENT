package com.example.warranty.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import java.time.LocalDate;

import lombok.Data;

@Data
public class ClaimRequestDto {
    @NotNull(message = "Warranty ID is required")
    private Long warrantyId;

    @NotNull(message = "Claim date is required")
    private LocalDate claimDate;

    @NotBlank(message = "Issue description is required")
    private String issueDescription;
}
