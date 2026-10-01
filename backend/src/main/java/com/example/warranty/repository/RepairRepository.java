package com.example.warranty.repository;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.example.warranty.entity.RepairEntity;

@Repository
public interface RepairRepository extends JpaRepository<RepairEntity, Long> {
    List<RepairEntity> findByUserId(Long userId);
    List<RepairEntity> findByProductId(Long productId);
}
