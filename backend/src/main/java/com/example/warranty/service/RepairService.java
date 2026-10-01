package com.example.warranty.service;

import java.util.List;

import com.example.warranty.dto.RepairRequestDto;
import com.example.warranty.dto.RepairResponseDto;

public interface RepairService {
    RepairResponseDto create(RepairRequestDto request);
    RepairResponseDto update(Long id, RepairRequestDto request);
    List<RepairResponseDto> getAll();
    List<RepairResponseDto> getByUserId(Long userId);
}
