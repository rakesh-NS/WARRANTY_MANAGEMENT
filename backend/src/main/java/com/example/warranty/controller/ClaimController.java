package com.example.warranty.controller;

import com.example.warranty.dto.ClaimRequestDto;
import com.example.warranty.dto.ClaimResponseDto;
import com.example.warranty.service.ClaimService;

import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/claims")
public class ClaimController {

    private final ClaimService claimService;

    public ClaimController(ClaimService claimService) {
        this.claimService = claimService;
    }

    @PostMapping
    public ResponseEntity<ClaimResponseDto> submit(@Valid @RequestBody ClaimRequestDto request) {
        return ResponseEntity.ok(claimService.submit(request));
    }

    @GetMapping
    public ResponseEntity<List<ClaimResponseDto>> getAll() {
        return ResponseEntity.ok(claimService.getAll());
    }

    @PutMapping("/{id}")
    public ResponseEntity<ClaimResponseDto> updateStatus(@PathVariable Long id,
                                                          @RequestParam String status,
                                                          @RequestParam(required = false) String adminRemarks) {
        return ResponseEntity.ok(claimService.updateStatus(id, status, adminRemarks));
    }
}
