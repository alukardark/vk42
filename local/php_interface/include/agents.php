<?php

/**
 * Обновляет изображения брендов с помощью запроса в 1С
 */
function updateBrandsPictures()
{
    \CCatalogExt::updateBrandsPictures();
    return 'updateBrandsPictures();';
}

/**
 * Обновляет кастомный индекс сортировки
 */
function updateSortProp()
{
    \CCatalogExt::updateSortProp();
    return 'updateSortProp();';
}

/**
 * Обновляет ифну по складам (название, адрес, координаты и т.п.)
 */
function updateStoresInfo()
{
    \CCatalogExt::updateStoresInfo();
    return 'updateStoresInfo();';
}

/**
 * Получает список складов из 1С, и если какого-либо склада на сайте нет - создает его
 */
function actualizeStoresList()
{
    \CCatalogExt::actualizeStoresList();
    return 'actualizeStoresList();';
}

/**
 * Генерирует sitemap для инфоблока шин
 */
function generateSiteMapTires()
{
    return 'generateSiteMapTires();';
}

/**
 * Обновляет услуги с помощью запроса в 1С
 */
function updateServices()
{
    \CServicesExt::updateServices();
    return 'updateServices();';
}

/**
 * Обновляет изображения брендов с помощью запроса в 1С
 */
function generateYandexFeed()
{
    \CXmlExt::generateYandexFeed();
    return 'generateYandexFeed();';
}

/**
 * Обновляет изображения брендов с помощью запроса в 1С
 */
function generateCatalogSitemap()
{
    \CXmlExt::generateSitemap();
    return 'generateCatalogSitemap();';
}

/**
 * Получает список операций из 1С
 */
//function getOperations()
//{
//    \CServicesExt::getOperations();
//    return 'getOperations();';
//}
