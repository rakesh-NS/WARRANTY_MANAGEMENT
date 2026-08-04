package com.example.warranty.dto;

import lombok.Data;
import java.time.LocalDate;

@Data
public class WarrantyResponseDto {
    private Long id;
    private Long productId;
    private Long userId;
    private LocalDate startDate;
    private LocalDate expiryDate;
    private String status;
    private String productName;
}
