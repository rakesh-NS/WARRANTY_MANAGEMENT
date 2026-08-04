package com.example.warranty.service;

import com.example.warranty.dto.LoginRequestDto;
import com.example.warranty.dto.LoginResponseDto;
import com.example.warranty.dto.UserRequestDto;
import com.example.warranty.dto.UserResponseDto;

public interface UserService {
    UserResponseDto register(UserRequestDto request);
    LoginResponseDto login(LoginRequestDto request);
    UserResponseDto getById(Long id);
    UserResponseDto update(Long id, UserRequestDto request);
    long getAllUsersCount();
}
