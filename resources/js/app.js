import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse'
Alpine.plugin(Collapse)
window.Alpine = Alpine;
Alpine.start();
import { initPriceRange, initDateFilter, initPagination } from './manager_product.js';
import { initCategorySearch } from './manager_category.js';

document.addEventListener('alpine:init', () => {
    initCategorySearch();
    initPriceRange();
    initDateFilter();
    initPagination();
});

