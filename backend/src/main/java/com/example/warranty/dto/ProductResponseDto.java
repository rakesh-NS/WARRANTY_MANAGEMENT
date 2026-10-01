package com.example.warranty.dto;

import java.math.BigDecimal;
import java.time.LocalDate;

import lombok.Data;

@Data
public class ProductResponseDto {
    private Long id;
    private String productName;
    private String brand;
    private String model;
    private String serialNumber;
    private LocalDate purchaseDate;
    private BigDecimal price;
    private String category;
    private String sellerName;
    private String invoiceNumber;
    private String description;
    private Long userId;
    private String userName;
}
