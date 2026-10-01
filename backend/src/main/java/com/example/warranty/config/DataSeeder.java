package com.example.warranty.config;

import java.math.BigDecimal;
import java.time.LocalDate;

import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;

import com.example.warranty.entity.ProductEntity;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.entity.WarrantyEntity;
import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;

@Configuration
public class DataSeeder {

    @Bean
    CommandLineRunner seedDemoUsersAndProducts(UserRepository userRepository,
                                               ProductRepository productRepository,
                                               WarrantyRepository warrantyRepository) {
        return args -> {
            BCryptPasswordEncoder encoder = new BCryptPasswordEncoder();

            UserEntity demoUser = createUserIfMissing(
                    userRepository,
                    encoder,
                    "demo.user@example.com",
                    "Demo User",
                    "demo123",
                    "+1 555 0101",
                    "CUSTOMER"
            );

            UserEntity adminUser = createUserIfMissing(
                    userRepository,
                    encoder,
                    "admin@example.com",
                    "Admin User",
                    "admin123",
                    "+1 555 0102",
                    "ADMIN"
            );

            createProductIfMissing(
                    warrantyRepository,
                    productRepository,
                    demoUser,
                    "Dell XPS 13",
                    "Dell",
                    "XPS 13",
                    "DXPS-1001",
                    LocalDate.now().minusMonths(5),
                    new BigDecimal("1499.99")
            );

            createProductIfMissing(
                    warrantyRepository,
                    productRepository,
                    demoUser,
                    "Apple MacBook Air",
                    "Apple",
                    "MacBook Air",
                    "MBA-2024-01",
                    LocalDate.now().minusMonths(8),
                    new BigDecimal("1899.00")
            );

            createProductIfMissing(
                    warrantyRepository,
                    productRepository,
                    adminUser,
                    "Samsung Galaxy S24",
                    "Samsung",
                    "Galaxy S24",
                    "SGS24-880",
                    LocalDate.now().minusMonths(2),
                    new BigDecimal("899.00")
            );
        };
    }

    private UserEntity createUserIfMissing(
            UserRepository userRepository,
            BCryptPasswordEncoder encoder,
            String email,
            String name,
            String password,
            String phone,
            String role
    ) {
        return userRepository.findByEmail(email)
                .orElseGet(() -> userRepository.save(UserEntity.builder()
                        .name(name)
                        .email(email)
                        .password(encoder.encode(password))
                        .phone(phone)
                        .role(role)
                        .build()));
    }

    private void createProductIfMissing(
            WarrantyRepository warrantyRepository,
            ProductRepository productRepository,
            UserEntity user,
            String productName,
            String brand,
            String model,
            String serialNumber,
            LocalDate purchaseDate,
            BigDecimal price
    ) {
        ProductEntity existingProduct = productRepository.findAll().stream()
                .filter(product -> product.getSerialNumber().equalsIgnoreCase(serialNumber))
                .findFirst()
                .orElse(null);

        if (existingProduct == null) {
            ProductEntity product = ProductEntity.builder()
                    .productName(productName)
                    .brand(brand)
                    .model(model)
                    .serialNumber(serialNumber)
                    .purchaseDate(purchaseDate)
                    .price(price)
                    .user(user)
                    .build();
            existingProduct = productRepository.save(product);
        }

        createWarrantyIfMissing(warrantyRepository, existingProduct, user);
    }

    private void createWarrantyIfMissing(WarrantyRepository warrantyRepository, ProductEntity product, UserEntity user) {
        boolean warrantyExists = warrantyRepository.findAll().stream()
                .anyMatch(warranty -> warranty.getProduct() != null && warranty.getProduct().getId().equals(product.getId()));

        if (!warrantyExists) {
            LocalDate startDate = product.getPurchaseDate() != null ? product.getPurchaseDate() : LocalDate.now();
            LocalDate expiryDate = startDate.plusMonths(12);
            String status = LocalDate.now().isAfter(expiryDate) ? "EXPIRED" : "ACTIVE";

            WarrantyEntity warranty = WarrantyEntity.builder()
                    .product(product)
                    .user(user)
                    .startDate(startDate)
                    .expiryDate(expiryDate)
                    .status(status)
                    .build();

            warrantyRepository.save(warranty);
        }
    }
}
