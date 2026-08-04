package com.example.warranty.dto;

import lombok.Data;
import java.math.BigDecimal;
import java.time.LocalDate;

@Data
public class ProductResponseDto {
    private Long id;
    private String productName;
    private String brand;
    private String model;
    private String serialNumber;
    private LocalDate purchaseDate;
    private BigDecimal price;
    private Long userId;
    private String userName;
}
