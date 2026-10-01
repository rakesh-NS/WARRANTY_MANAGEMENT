package com.example.warranty.service.impl;

import java.util.List;
import java.util.stream.Collectors;

import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.example.warranty.dto.LoginRequestDto;
import com.example.warranty.dto.LoginResponseDto;
import com.example.warranty.dto.UserRequestDto;
import com.example.warranty.dto.UserResponseDto;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.exception.DuplicateResourceException;
import com.example.warranty.exception.InvalidLoginException;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.security.JwtService;
import com.example.warranty.service.UserService;

@Service
@Transactional
public class UserServiceImpl implements UserService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtService jwtService;

    public UserServiceImpl(UserRepository userRepository, PasswordEncoder passwordEncoder, JwtService jwtService) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.jwtService = jwtService;
    }

    private String normalizeEmail(String email) {
        return email == null ? null : email.trim().toLowerCase();
    }

    private String normalizeRole(String role) {
        if (role == null || role.isBlank()) {
            return "CUSTOMER";
        }
        String normalized = role.trim().toUpperCase();
        return "ADMIN".equals(normalized) ? "ADMIN" : "CUSTOMER";
    }

    @Override
    public UserResponseDto register(UserRequestDto request) {
        String normalizedEmail = normalizeEmail(request.getEmail());
        if (userRepository.existsByEmail(normalizedEmail)) {
            throw new DuplicateResourceException("Email already exists");
        }
        UserEntity entity = UserEntity.builder()
                .name(request.getName())
                .email(normalizedEmail)
                .password(passwordEncoder.encode(request.getPassword()))
                .phone(request.getPhone())
                .role("CUSTOMER")
                .build();
        UserEntity saved = userRepository.save(entity);
        return toResponse(saved);
    }

    @Override
    public LoginResponseDto login(LoginRequestDto request) {
        String normalizedEmail = normalizeEmail(request.getEmail());
        UserEntity user = userRepository.findByEmail(normalizedEmail)
                .orElseThrow(() -> new InvalidLoginException("Invalid email or password"));
        if (!passwordEncoder.matches(request.getPassword(), user.getPassword())) {
            throw new InvalidLoginException("Invalid email or password");
        }
        String normalizedRole = normalizeRole(user.getRole());
        if (!"ADMIN".equals(normalizedRole)) {
            normalizedRole = "CUSTOMER";
            user.setRole(normalizedRole);
        }
        String token = jwtService.generateToken(user.getEmail(), normalizedRole, user.getId());
        return new LoginResponseDto(user.getId(), user.getName(), user.getEmail(), normalizedRole, token);
    }

    @Override
    public UserResponseDto getById(Long id) {
        return userRepository.findById(id)
                .map(this::toResponse)
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + id));
    }

    @Override
    public List<UserResponseDto> getAll() {
        return userRepository.findAll().stream()
                .map(this::toResponse)
                .collect(Collectors.toList());
    }

    @Override
    public UserResponseDto update(Long id, UserRequestDto request) {
        UserEntity user = userRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + id));
        String normalizedEmail = normalizeEmail(request.getEmail());
        if (!user.getEmail().equalsIgnoreCase(normalizedEmail) && userRepository.existsByEmail(normalizedEmail)) {
            throw new DuplicateResourceException("Email already exists");
        }
        user.setName(request.getName());
        user.setEmail(normalizedEmail);
        if (request.getPassword() != null && !request.getPassword().isBlank()) {
            user.setPassword(passwordEncoder.encode(request.getPassword()));
        }
        user.setPhone(request.getPhone());
        user.setRole(normalizeRole(user.getRole()));
        return toResponse(userRepository.save(user));
    }

    @Override
    public long getAllUsersCount() {
        return userRepository.count();
    }

    private UserResponseDto toResponse(UserEntity user) {
        UserResponseDto response = new UserResponseDto();
        response.setId(user.getId());
        response.setName(user.getName());
        response.setEmail(user.getEmail());
        response.setPhone(user.getPhone());
        response.setRole(normalizeRole(user.getRole()));
        return response;
    }
}
