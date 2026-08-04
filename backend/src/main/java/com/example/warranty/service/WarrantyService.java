package com.example.warranty.service;

import com.example.warranty.dto.WarrantyRequestDto;
import com.example.warranty.dto.WarrantyResponseDto;

import java.util.List;

public interface WarrantyService {
    List<WarrantyResponseDto> getAll();
    WarrantyResponseDto save(WarrantyRequestDto request);
    WarrantyResponseDto update(Long id, WarrantyRequestDto request);
}
