package com.example.warranty.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import lombok.Data;

@Data
public class RepairRequestDto {
    @NotNull(message = "Claim ID is required")
    private Long claimId;

    @NotBlank(message = "Status is required")
    private String status;

    private String technician;
    private String remarks;
}
