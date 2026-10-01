package com.example.warranty.controller;

import java.util.List;

import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.example.warranty.dto.RepairRequestDto;
import com.example.warranty.dto.RepairResponseDto;
import com.example.warranty.service.RepairService;

import jakarta.validation.Valid;

@RestController
@RequestMapping("/api/repairs")
public class RepairController {

    private final RepairService repairService;

    public RepairController(RepairService repairService) {
        this.repairService = repairService;
    }

    @GetMapping
    public ResponseEntity<List<RepairResponseDto>> getAll() {
        return ResponseEntity.ok(repairService.getAll());
    }

    @GetMapping("/user/{userId}")
    public ResponseEntity<List<RepairResponseDto>> getByUserId(@PathVariable Long userId) {
        return ResponseEntity.ok(repairService.getByUserId(userId));
    }

    @PostMapping
    public ResponseEntity<RepairResponseDto> create(@Valid @RequestBody RepairRequestDto request) {
        return ResponseEntity.ok(repairService.create(request));
    }

    @PutMapping("/{id}")
    public ResponseEntity<RepairResponseDto> update(@PathVariable Long id, @Valid @RequestBody RepairRequestDto request) {
        return ResponseEntity.ok(repairService.update(id, request));
    }
}
