package com.example.warranty.service.impl;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;
import java.util.stream.Collectors;

import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.example.warranty.dto.ProductRequestDto;
import com.example.warranty.dto.ProductResponseDto;
import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;
import com.example.warranty.security.AuthUser;
import com.example.warranty.service.ProductService;

@Service
@Transactional
public class ProductServiceImpl implements ProductService {

    private final ProductRepository productRepository;
    private final UserRepository userRepository;
    private final WarrantyRepository warrantyRepository;

    public ProductServiceImpl(ProductRepository productRepository, UserRepository userRepository, WarrantyRepository warrantyRepository) {
        this.productRepository = productRepository;
        this.userRepository = userRepository;
        this.warrantyRepository = warrantyRepository;
    }

    @Override
    public List<ProductResponseDto> getAll() {
        UserEntity currentUser = getCurrentUser();
        List<ProductEntity> products = "ADMIN".equalsIgnoreCase(currentUser.getRole())
                ? productRepository.findAll()
                : productRepository.findByUserId(currentUser.getId());
        return products.stream().map(this::toResponse).collect(Collectors.toList());
    }

    @Override
    public ProductResponseDto getById(Long id) {
        return productRepository.findById(id).map(this::toResponse)
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + id));
    }

    @Override
    public ProductResponseDto save(ProductRequestDto request) {
        UserEntity user = getCurrentUser();
        validateProductRequest(request);

        if (request.getUserId() != null && !request.getUserId().equals(user.getId())) {
            throw new AccessDeniedException("You cannot register a product for another customer.");
        }

        ProductEntity entity = ProductEntity.builder()
                .productName(request.getProductName())
                .brand(request.getBrand())
                .model(request.getModel())
                .serialNumber(request.getSerialNumber().trim())
                .purchaseDate(request.getPurchaseDate())
                .price(request.getPrice())
                .category(request.getCategory())
                .sellerName(request.getSellerName())
                .invoiceNumber(request.getInvoiceNumber())
                .description(request.getDescription())
                .user(user)
                .build();
        ProductEntity savedProduct = productRepository.save(entity);
        ensureWarrantyForProduct(savedProduct);
        return toResponse(savedProduct);
    }

    @Override
    public ProductResponseDto update(Long id, ProductRequestDto request) {
        ProductEntity product = productRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("Product not found with id: " + id));

        UserEntity currentUser = getCurrentUser();
        if (!product.getUser().getId().equals(currentUser.getId()) && !"ADMIN".equalsIgnoreCase(currentUser.getRole())) {
            throw new AccessDeniedException("You cannot update this product.");
        }
        validateProductRequest(request);

        product.setProductName(request.getProductName());
        product.setBrand(request.getBrand());
        product.setModel(request.getModel());
        product.setSerialNumber(request.getSerialNumber().trim());
        product.setPurchaseDate(request.getPurchaseDate());
        product.setPrice(request.getPrice());
        product.setCategory(request.getCategory());
        product.setSellerName(request.getSellerName());
        product.setInvoiceNumber(request.getInvoiceNumber());
        product.setDescription(request.getDescription());
        product.setUser(product.getUser());
        ProductEntity savedProduct = productRepository.save(product);
        ensureWarrantyForProduct(savedProduct);
        return toResponse(savedProduct);
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

    private void ensureWarrantyForProduct(ProductEntity product) {
        if (product.getWarranty() != null) {
            WarrantyEntity warranty = product.getWarranty();
            warranty.setUser(product.getUser());
            warranty.setStartDate(product.getPurchaseDate());
            warranty.setExpiryDate(product.getPurchaseDate().plusMonths(12));
            warranty.setWarrantyType("Standard Warranty");
            warranty.setStatus(calculateWarrantyStatus(product.getPurchaseDate().plusMonths(12)));
            warrantyRepository.save(warranty);
            return;
        }

        LocalDate startDate = product.getPurchaseDate() != null ? product.getPurchaseDate() : LocalDate.now();
        LocalDate expiryDate = startDate.plusMonths(12);

        WarrantyEntity warranty = WarrantyEntity.builder()
                .product(product)
                .user(product.getUser())
                .startDate(startDate)
                .expiryDate(expiryDate)
                .status(calculateWarrantyStatus(expiryDate))
                .warrantyType("Standard Warranty")
                .build();

        product.setWarranty(warrantyRepository.save(warranty));
    }

    private String calculateWarrantyStatus(LocalDate expiryDate) {
        if (expiryDate == null) {
            return "ACTIVE";
        }
        if (LocalDate.now().isAfter(expiryDate)) {
            return "EXPIRED";
        }
        return "ACTIVE";
    }

    private void validateProductRequest(ProductRequestDto request) {
        if (request.getPurchaseDate() != null && request.getPurchaseDate().isAfter(LocalDate.now())) {
            throw new IllegalArgumentException("Purchase date cannot be a future date.");
        }
        if (request.getPrice() == null || request.getPrice().compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("Price must be greater than zero.");
        }
        if (request.getSerialNumber() == null || request.getSerialNumber().isBlank()) {
            throw new IllegalArgumentException("Serial number is required.");
        }
        if (productRepository.existsBySerialNumberIgnoreCase(request.getSerialNumber().trim())) {
            throw new IllegalArgumentException("Serial number must be unique.");
        }
    }

    private UserEntity getCurrentUser() {
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        if (authentication == null || !authentication.isAuthenticated() || authentication.getPrincipal() instanceof String) {
            throw new AccessDeniedException("Authentication required.");
        }
        AuthUser authUser = (AuthUser) authentication.getPrincipal();
        return userRepository.findById(authUser.getId())
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + authUser.getId()));
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
        response.setCategory(product.getCategory());
        response.setSellerName(product.getSellerName());
        response.setInvoiceNumber(product.getInvoiceNumber());
        response.setDescription(product.getDescription());
        if (product.getUser() != null) {
            response.setUserId(product.getUser().getId());
            response.setUserName(product.getUser().getName());
        }
        return response;
    }
}
