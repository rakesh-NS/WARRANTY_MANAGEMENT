package com.example.warranty.service;

import com.example.warranty.dto.ProductRequestDto;
import com.example.warranty.dto.ProductResponseDto;

import java.util.List;

public interface ProductService {
    List<ProductResponseDto> getAll();
    ProductResponseDto getById(Long id);
    ProductResponseDto save(ProductRequestDto request);
    ProductResponseDto update(Long id, ProductRequestDto request);
    void delete(Long id);
    List<ProductResponseDto> search(String query);
}
