package com.example.warranty.service;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertFalse;
import static org.junit.jupiter.api.Assertions.assertNotNull;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;

import com.example.warranty.dto.LoginRequestDto;
import com.example.warranty.dto.LoginResponseDto;
import com.example.warranty.dto.UserRequestDto;
import com.example.warranty.dto.UserResponseDto;

@SpringBootTest
class UserServiceImplTest {

    @Autowired
    private UserService userService;

    @Test
    void register_shouldPersistUserToLocalDatabase() {
        UserRequestDto request = new UserRequestDto();
        request.setName("Local Demo User");
        request.setEmail("local-demo-" + System.currentTimeMillis() + "@example.com");
        request.setPassword("demoPass123");
        request.setPhone("1234567890");
        request.setRole("USER");

        UserResponseDto response = userService.register(request);

        assertNotNull(response.getId());
        assertEquals(request.getName(), response.getName());
        assertEquals(request.getEmail(), response.getEmail());
        assertEquals("CUSTOMER", response.getRole());
    }

    @Test
    void register_shouldForceCustomerRole() {
        UserRequestDto request = new UserRequestDto();
        request.setName("Forced Customer");
        request.setEmail("forced-customer-" + System.currentTimeMillis() + "@example.com");
        request.setPassword("demoPass123");
        request.setPhone("1234567890");
        request.setRole("ADMIN");

        UserResponseDto response = userService.register(request);

        assertEquals("CUSTOMER", response.getRole());
    }

    @Test
    void login_shouldAcceptUppercaseEmail() {
        LoginRequestDto request = new LoginRequestDto();
        request.setEmail("DEMO.USER@EXAMPLE.COM");
        request.setPassword("demo123");

        LoginResponseDto response = userService.login(request);

        assertNotNull(response.getToken());
        assertEquals("Demo User", response.getName());
        assertEquals("CUSTOMER", response.getRole());
    }

    @Test
    void getAll_shouldReturnNormalizedUsers() {
        var users = userService.getAll();

        assertNotNull(users);
        assertFalse(users.isEmpty());
        assertEquals("CUSTOMER", users.get(0).getRole());
    }
}
