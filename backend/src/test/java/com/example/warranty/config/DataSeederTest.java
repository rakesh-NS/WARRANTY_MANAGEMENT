package com.example.warranty.config;

import static org.junit.jupiter.api.Assertions.assertTrue;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;

import com.example.warranty.repository.ProductRepository;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.repository.WarrantyRepository;

@SpringBootTest
class DataSeederTest {

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private ProductRepository productRepository;

    @Autowired
    private WarrantyRepository warrantyRepository;

    @Test
    void shouldSeedDemoUserProductsAndWarrantyRecordsOnStartup() {
        assertTrue(userRepository.existsByEmail("demo.user@example.com"));
        assertTrue(productRepository.count() > 0);
        assertTrue(warrantyRepository.count() > 0);
    }
}
