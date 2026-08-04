package com.example.warranty.dto;

import lombok.AllArgsConstructor;
import lombok.Data;

@Data
@AllArgsConstructor
public class LoginResponseDto {
    private Long id;
    private String name;
    private String email;
    private String role;
    private String token;
}
