package com.example.warranty.service;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertNotNull;
import static org.junit.jupiter.api.Assertions.assertThrows;

import java.math.BigDecimal;
import java.time.LocalDate;

import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;

import com.example.warranty.dto.ClaimRequestDto;
import com.example.warranty.dto.RepairRequestDto;
import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.exception.WarrantyExpiredException;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;

@SpringBootTest
class ClaimAndRepairLifecycleTest {

    @Autowired
    private ClaimService claimService;

    @Autowired
    private RepairService repairService;

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private ProductRepository productRepository;

    @Autowired
    private WarrantyRepository warrantyRepository;

    @Test
    void shouldRejectClaimForExpiredWarrantyAndCreateRepairWhenApproved() {
        UserEntity user = userRepository.save(UserEntity.builder()
                .name("Lifecycle User")
                .email("lifecycle.user@example.com")
                .password("secret")
                .phone("123456")
                .role("USER")
                .build());

        ProductEntity product = productRepository.save(ProductEntity.builder()
                .productName("Test Lamp")
                .brand("Acme")
                .model("L1")
                .serialNumber("LAMP-1001")
                .purchaseDate(LocalDate.now().minusMonths(15))
                .price(new BigDecimal("299.99"))
                .user(user)
                .build());

        WarrantyEntity warranty = warrantyRepository.save(WarrantyEntity.builder()
                .product(product)
                .user(user)
                .startDate(product.getPurchaseDate())
                .expiryDate(product.getPurchaseDate().plusMonths(12))
                .status("EXPIRED")
                .build());

        ClaimRequestDto claimRequest = new ClaimRequestDto();
        claimRequest.setWarrantyId(warranty.getId());
        claimRequest.setClaimDate(LocalDate.now());
        claimRequest.setIssueDescription("Screen flickers");

        assertThrows(WarrantyExpiredException.class, () -> claimService.submit(claimRequest));

        warranty.setStatus("ACTIVE");
        warrantyRepository.save(warranty);

        var claim = claimService.submit(claimRequest);
        assertEquals("PENDING", claim.getStatus());

        var approved = claimService.updateStatus(claim.getId(), "APPROVED", "Approved after review");
        assertEquals("APPROVED", approved.getStatus());

        RepairRequestDto repairRequest = new RepairRequestDto();
        repairRequest.setClaimId(claim.getId());
        repairRequest.setStatus("SCHEDULED");
        repairRequest.setTechnician("Technician 1");
        repairRequest.setRemarks("Initial inspection");

        var repair = repairService.create(repairRequest);
        assertNotNull(repair.getId());
        assertEquals("SCHEDULED", repair.getStatus());
    }
}
