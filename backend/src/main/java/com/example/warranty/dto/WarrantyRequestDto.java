package com.example.warranty.dto;

import jakarta.validation.constraints.NotNull;
import java.time.LocalDate;

import lombok.Data;

@Data
public class WarrantyRequestDto {
    @NotNull(message = "Product ID is required")
    private Long productId;

    @NotNull(message = "User ID is required")
    private Long userId;

    @NotNull(message = "Start date is required")
    private LocalDate startDate;

    @NotNull(message = "Expiry date is required")
    private LocalDate expiryDate;

    @NotNull(message = "Status is required")
    private String status;
}
