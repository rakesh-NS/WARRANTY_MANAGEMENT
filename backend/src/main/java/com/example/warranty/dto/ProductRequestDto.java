package com.example.warranty.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import java.math.BigDecimal;
import java.time.LocalDate;

import lombok.Data;

@Data
public class ProductRequestDto {
    @NotBlank(message = "Product name is required")
    private String productName;

    @NotBlank(message = "Brand is required")
    private String brand;

    @NotBlank(message = "Model is required")
    private String model;

    @NotBlank(message = "Serial number is required")
    private String serialNumber;

    @NotNull(message = "Purchase date is required")
    private LocalDate purchaseDate;

    @NotNull(message = "Price is required")
    private BigDecimal price;

    @NotNull(message = "User ID is required")
    private Long userId;
}
