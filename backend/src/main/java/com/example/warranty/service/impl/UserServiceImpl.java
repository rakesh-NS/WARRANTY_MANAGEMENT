package com.example.warranty.service.impl;

import com.example.warranty.dto.LoginRequestDto;
import com.example.warranty.dto.LoginResponseDto;
import com.example.warranty.dto.UserRequestDto;
import com.example.warranty.dto.UserResponseDto;
import com.example.warranty.entity.UserEntity;
import com.example.warranty.exception.DuplicateResourceException;
import com.example.warranty.exception.InvalidLoginException;
import com.example.warranty.exception.ResourceNotFoundException;
import com.example.warranty.repository.UserRepository;
import com.example.warranty.service.UserService;

import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class UserServiceImpl implements UserService {

    private final UserRepository userRepository;
    private final BCryptPasswordEncoder passwordEncoder;

    public UserServiceImpl(UserRepository userRepository) {
        this.userRepository = userRepository;
        this.passwordEncoder = new BCryptPasswordEncoder();
    }

    @Override
    public UserResponseDto register(UserRequestDto request) {
        if (userRepository.existsByEmail(request.getEmail())) {
            throw new DuplicateResourceException("Email already exists");
        }
        UserEntity entity = UserEntity.builder()
                .name(request.getName())
                .email(request.getEmail())
                .password(passwordEncoder.encode(request.getPassword()))
                .phone(request.getPhone())
                .role(request.getRole())
                .build();
        UserEntity saved = userRepository.save(entity);
        return toResponse(saved);
    }

    @Override
    public LoginResponseDto login(LoginRequestDto request) {
        UserEntity user = userRepository.findByEmail(request.getEmail())
                .orElseThrow(() -> new InvalidLoginException("Invalid email or password"));
        if (!passwordEncoder.matches(request.getPassword(), user.getPassword())) {
            throw new InvalidLoginException("Invalid email or password");
        }
        return new LoginResponseDto(user.getId(), user.getName(), user.getEmail(), user.getRole(), "token-placeholder");
    }

    @Override
    public UserResponseDto getById(Long id) {
        return userRepository.findById(id)
                .map(this::toResponse)
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + id));
    }

    @Override
    public UserResponseDto update(Long id, UserRequestDto request) {
        UserEntity user = userRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + id));
        if (!user.getEmail().equals(request.getEmail()) && userRepository.existsByEmail(request.getEmail())) {
            throw new DuplicateResourceException("Email already exists");
        }
        user.setName(request.getName());
        user.setEmail(request.getEmail());
        if (request.getPassword() != null && !request.getPassword().isBlank()) {
            user.setPassword(passwordEncoder.encode(request.getPassword()));
        }
        user.setPhone(request.getPhone());
        user.setRole(request.getRole());
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
        response.setRole(user.getRole());
        return response;
    }
}
