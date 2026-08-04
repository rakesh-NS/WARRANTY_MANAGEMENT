package com.example.warranty.controller;

import com.example.warranty.dto.WarrantyRequestDto;
import com.example.warranty.dto.WarrantyResponseDto;
import com.example.warranty.service.WarrantyService;

import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/warranty")
public class WarrantyController {

    private final WarrantyService warrantyService;

    public WarrantyController(WarrantyService warrantyService) {
        this.warrantyService = warrantyService;
    }

    @GetMapping
    public ResponseEntity<List<WarrantyResponseDto>> getAll() {
        return ResponseEntity.ok(warrantyService.getAll());
    }

    @PostMapping
    public ResponseEntity<WarrantyResponseDto> create(@Valid @RequestBody WarrantyRequestDto request) {
        return ResponseEntity.ok(warrantyService.save(request));
    }

    @PutMapping("/{id}")
    public ResponseEntity<WarrantyResponseDto> update(@PathVariable Long id, @Valid @RequestBody WarrantyRequestDto request) {
        return ResponseEntity.ok(warrantyService.update(id, request));
    }
}
