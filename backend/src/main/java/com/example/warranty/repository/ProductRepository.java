package com.example.warranty.repository;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.example.warranty.entity.ProductEntity;

@Repository
public interface ProductRepository extends JpaRepository<ProductEntity, Long> {
    List<ProductEntity> findByProductNameContainingIgnoreCaseOrBrandContainingIgnoreCaseOrModelContainingIgnoreCase(String productName, String brand, String model);
}
