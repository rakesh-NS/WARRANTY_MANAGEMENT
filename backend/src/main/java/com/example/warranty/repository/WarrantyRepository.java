package com.example.warranty.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.example.warranty.entity.WarrantyEntity;

@Repository
public interface WarrantyRepository extends JpaRepository<WarrantyEntity, Long> {
}
