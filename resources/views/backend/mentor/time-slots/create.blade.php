@extends('backend.layouts.app')

@section('title', 'Create Time Slot')
@section('pageTitle', 'Create New Course')
@section('backUrl', route('mentor.dashboard'))

@section('header')
                            <div class="flex items-center space-x-3">
                            
                            <a href="" class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition-colors">
                                <i class="fa-solid fa-chevron-left text-sm"></i>
                                <span class="text-sm font-medium">Back</span>
                            </a>
                            
                            <h1 class="text-xl font-semibold text-gray-900 ml-2"></h1>
                            
                            <p class="text-sm text-gray-500"></p>
                            
                        </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Main Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form class="space-y-8">
            <!-- Date Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input type="date" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           value="10/06/2025">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" 
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Category Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option>E-Commerce & Online Stores</option>
                        <option>Web Development</option>
                        <option>Mobile Development</option>
                        <option>Digital Marketing</option>
                        <option>UI/UX Design</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sub Category *</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option>Dropshipping & E-Commerce</option>
                        <option>Shopify Development</option>
                        <option>WooCommerce</option>
                        <option>Amazon FBA</option>
                    </select>
                </div>
            </div>

            <!-- Time Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input type="time" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           value="10:00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="time" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           value="17:00">
                </div>
            </div>

            <!-- Description (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                <textarea rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Add any additional details about this time slot..."></textarea>
            </div>

            <!-- Available Days Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">Available Days</label>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Monday</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Tuesday</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Wednesday</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Thursday</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Friday</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Saturday</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Sunday</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                <button type="button" 
                        class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-full sm:w-auto bg-gradient-to-r from-purple-500 to-pink-500 text-white px-8 py-3 rounded-lg hover:shadow-lg focus:ring-2 focus:ring-purple-500 transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Time Slot</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tips Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-lightbulb text-blue-600 text-lg"></i>
            </div>
            <div>
                <h4 class="text-sm font-medium text-blue-900 mb-2">Tips for creating effective time slots:</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Set realistic pricing based on your expertise and market rates</li>
                    <li>• Choose categories that match your skills and experience</li>
                    <li>• Consider your time zone when setting available hours</li>
                    <li>• Leave buffer time between sessions for preparation</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
