package com.example.warranty.repository;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.example.warranty.entity.ClaimEntity;

@Repository
public interface ClaimRepository extends JpaRepository<ClaimEntity, Long> {
    List<ClaimEntity> findByWarrantyId(Long warrantyId);
    List<ClaimEntity> findByWarrantyProductId(Long productId);
}
