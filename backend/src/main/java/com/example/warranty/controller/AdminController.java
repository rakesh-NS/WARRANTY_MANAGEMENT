package com.example.warranty.controller;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.example.warranty.dto.LoginRequestDto;
import com.example.warranty.dto.LoginResponseDto;
import com.example.warranty.dto.UserResponseDto;
import com.example.warranty.exception.InvalidLoginException;
import com.example.warranty.service.ClaimService;
import com.example.warranty.service.ProductService;
import com.example.warranty.service.UserService;
import com.example.warranty.service.WarrantyService;

@RestController
@RequestMapping("/api/admin")
public class AdminController {

    private final UserService userService;
    private final ProductService productService;
    private final WarrantyService warrantyService;
    private final ClaimService claimService;

    public AdminController(UserService userService, ProductService productService, WarrantyService warrantyService, ClaimService claimService) {
        this.userService = userService;
        this.productService = productService;
        this.warrantyService = warrantyService;
        this.claimService = claimService;
    }

    @PostMapping("/login")
    public ResponseEntity<LoginResponseDto> login(@RequestBody LoginRequestDto request) {
        LoginResponseDto response = userService.login(request);
        if (!"ADMIN" .equalsIgnoreCase(response.getRole())) {
            throw new InvalidLoginException("Invalid admin credentials");
        }
        return ResponseEntity.ok(response);
    }

    @GetMapping("/users")
    public ResponseEntity<List<UserResponseDto>> getUsers() {
        return ResponseEntity.ok(userService.getAll());
    }

    @GetMapping("/dashboard")
    public ResponseEntity<Map<String, Object>> dashboard() {
        Map<String, Object> stats = new HashMap<>();
        stats.put("totalUsers", userService.getAllUsersCount());
        stats.put("totalProducts", productService.getAll().size());
        stats.put("activeWarranties", warrantyService.getAll().stream().filter(w -> "ACTIVE".equalsIgnoreCase(w.getStatus())).count());
        stats.put("expiredWarranties", warrantyService.getAll().stream().filter(w -> "EXPIRED".equalsIgnoreCase(w.getStatus())).count());
        stats.put("pendingClaims", claimService.getAll().stream().filter(c -> "PENDING".equalsIgnoreCase(c.getStatus())).count());
        stats.put("approvedClaims", claimService.getAll().stream().filter(c -> "APPROVED".equalsIgnoreCase(c.getStatus())).count());
        stats.put("rejectedClaims", claimService.getAll().stream().filter(c -> "REJECTED".equalsIgnoreCase(c.getStatus())).count());
        return ResponseEntity.ok(stats);
    }
}
