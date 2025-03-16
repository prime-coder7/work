from django.contrib import admin
from django.urls import path
from api.views import *
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    # Category Endpoints
    path('getCategories', getCategories, name='getCategories'),
    path('getCategory/<int:id>/', getCategory, name='getCategory'),
    path('addCategory', addCategory, name='addCategory'),
    path('updateCategory/<int:id>/', updateCategory, name='updateCategory'),
    path('deleteCategory/<int:id>/', deleteCategory, name='deleteCategory'),

    # Product Endpoints
    path('getProducts', getProducts, name='getProducts'),
    path('getProduct/<int:id>/', getProduct, name='getProduct'),
    path('getProductsByCategory/<int:c_id>/', getProductsByCategory, name='getProductsByCategory'),
    path('addProduct', addProduct, name='addProduct'),
    path('updateProduct/<int:id>/', updateProduct, name='updateProduct'),
    path('deleteProduct/<int:id>/', deleteProduct, name='deleteProduct'),

    # Cart Endpoints
    path('getCart', getCart, name='getCart'),
    path('addToCart', addToCart, name='addToCart'),
    # path('removeFromCart/<int:item_id>/', removeFromCart, name='removeFromCart'),
    # path('updateCartItem/<int:item_id>/', updateCartItem, name='updateCartItem'),
    # path('clearCart', clearCart, name='clearCart'),

    # Order Endpoints
    # path('getOrders', getOrders, name='getOrders'),
    # path('createOrder', createOrder, name='createOrder'),
    # path('getOrder/<int:id>/', getOrder, name='getOrder'),
    # path('updateOrderStatus/<int:id>/', updateOrderStatus, name='updateOrderStatus'),
    # path('cancelOrder/<int:id>/', cancelOrder, name='cancelOrder'),

    # Authentication and User Endpoints
    # path('register', registerUser, name='registerUser'),
    # path('login', loginUser, name='loginUser'),
    # path('logout', logoutUser, name='logoutUser'),
    # path('getUserProfile', getUserProfile, name='getUserProfile'),
    # path('updateUserProfile', updateUserProfile, name='updateUserProfile'),

    # Search and Filter Endpoints
    # path('searchProducts', searchProducts, name='searchProducts'),
    # path('filterProducts', filterProducts, name='filterProducts'),

    # Checkout and Payment Endpoints
    # path('checkout', checkout, name='checkout'),
    # path('payment', payment, name='payment'),
    # path('paymentStatus', paymentStatus, name='paymentStatus'),
]

# Serve media files during development
if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
