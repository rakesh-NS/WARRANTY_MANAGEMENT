package com.example.warranty.service.impl;

import com.example.warranty.dto.ProductRequestDto;
import com.example.warranty.dto.ProductResponseDto;
import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.service.ProductService;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.stream.Collectors;

@Service
@Transactional
public class ProductServiceImpl implements ProductService {

    private final ProductRepository productRepository;
    private final UserRepository userRepository;

    public ProductServiceImpl(ProductRepository productRepository, UserRepository userRepository) {
        this.productRepository = productRepository;
        this.userRepository = userRepository;
    }

    @Override
    public List<ProductResponseDto> getAll() {
        return productRepository.findAll().stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public ProductResponseDto getById(Long id) {
        return productRepository.findById(id).map(this::toResponse)
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + id));
    }

    @Override
    public ProductResponseDto save(ProductRequestDto request) {
        UserEntity user = userRepository.findById(request.getUserId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + request.getUserId()));
        ProductEntity entity = ProductEntity.builder()
                .productName(request.getProductName())
                .brand(request.getBrand())
                .model(request.getModel())
                .serialNumber(request.getSerialNumber())
                .purchaseDate(request.getPurchaseDate())
                .price(request.getPrice())
                .user(user)
                .build();
        return toResponse(productRepository.save(entity));
    }

    @Override
    public ProductResponseDto update(Long id, ProductRequestDto request) {
        ProductEntity product = productRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + id));
        UserEntity user = userRepository.findById(request.getUserId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + request.getUserId()));
        product.setProductName(request.getProductName());
        product.setBrand(request.getBrand());
        product.setModel(request.getModel());
        product.setSerialNumber(request.getSerialNumber());
        product.setPurchaseDate(request.getPurchaseDate());
        product.setPrice(request.getPrice());
        product.setUser(user);
        return toResponse(productRepository.save(product));
    }

    @Override
    public void delete(Long id) {
        ProductEntity product = productRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + id));
        productRepository.delete(product);
    }

    @Override
    public List<ProductResponseDto> search(String query) {
        return productRepository.findByProductNameContainingIgnoreCaseOrBrandContainingIgnoreCaseOrModelContainingIgnoreCase(query, query, query)
                .stream()
                .map(this::toResponse)
                .collect(Collectors.toList());
    }

    private ProductResponseDto toResponse(ProductEntity product) {
        ProductResponseDto response = new ProductResponseDto();
        response.setId(product.getId());
        response.setProductName(product.getProductName());
        response.setBrand(product.getBrand());
        response.setModel(product.getModel());
        response.setSerialNumber(product.getSerialNumber());
        response.setPurchaseDate(product.getPurchaseDate());
        response.setPrice(product.getPrice());
        if (product.getUser() != null) {
            response.setUserId(product.getUser().getId());
            response.setUserName(product.getUser().getName());
        }
        return response;
    }
}
