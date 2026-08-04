package com.example.warranty.service.impl;

import com.example.warranty.dto.WarrantyRequestDto;
import com.example.warranty.dto.WarrantyResponseDto;
import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;
import com.example.warranty.service.WarrantyService;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.stream.Collectors;

@Service
@Transactional
public class WarrantyServiceImpl implements WarrantyService {

    private final WarrantyRepository warrantyRepository;
    private final ProductRepository productRepository;
    private final UserRepository userRepository;

    public WarrantyServiceImpl(WarrantyRepository warrantyRepository,
                               ProductRepository productRepository,
                               UserRepository userRepository) {
        this.warrantyRepository = warrantyRepository;
        this.productRepository = productRepository;
        this.userRepository = userRepository;
    }

    @Override
    public List<WarrantyResponseDto> getAll() {
        return warrantyRepository.findAll().stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public WarrantyResponseDto save(WarrantyRequestDto request) {
        ProductEntity product = productRepository.findById(request.getProductId())
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + request.getProductId()));
        UserEntity user = userRepository.findById(request.getUserId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + request.getUserId()));
        WarrantyEntity entity = WarrantyEntity.builder()
                .product(product)
                .user(user)
                .startDate(request.getStartDate())
                .expiryDate(request.getExpiryDate())
                .status(request.getStatus())
                .build();
        return toResponse(warrantyRepository.save(entity));
    }

    @Override
    public WarrantyResponseDto update(Long id, WarrantyRequestDto request) {
        WarrantyEntity warranty = warrantyRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Warranty not found with id: " + id));
        ProductEntity product = productRepository.findById(request.getProductId())
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + request.getProductId()));
        UserEntity user = userRepository.findById(request.getUserId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + request.getUserId()));
        warranty.setProduct(product);
        warranty.setUser(user);
        warranty.setStartDate(request.getStartDate());
        warranty.setExpiryDate(request.getExpiryDate());
        warranty.setStatus(request.getStatus());
        return toResponse(warrantyRepository.save(warranty));
    }

    private WarrantyResponseDto toResponse(WarrantyEntity warranty) {
        WarrantyResponseDto response = new WarrantyResponseDto();
        response.setId(warranty.getId());
        response.setProductId(warranty.getProduct().getId());
        response.setUserId(warranty.getUser().getId());
        response.setStartDate(warranty.getStartDate());
        response.setExpiryDate(warranty.getExpiryDate());
        response.setStatus(warranty.getStatus());
        response.setProductName(warranty.getProduct().getProductName());
        return response;
    }
}
