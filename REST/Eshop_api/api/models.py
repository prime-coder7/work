from django.db import models
from django.contrib.auth.models import User
from django.core.validators import MinValueValidator, MaxValueValidator

# Create your models here.

# Category Model
class Category(models.Model):
    categoryName = models.CharField(max_length=50, unique=True)
    categoryDescription = models.CharField(max_length=255, null=True, blank=True)
    categoryImage = models.ImageField(upload_to="category_images", blank=True, null=True)

    def __str__(self):
        return self.categoryName
    
# Product Model
class Product(models.Model):
    category = models.ForeignKey(Category, on_delete=models.CASCADE, related_name="products")
    productName = models.CharField(max_length=100)
    productPrice = models.DecimalField(max_digits=10, decimal_places=2)  # 💰 DecimalField for accurate pricing
    productQty = models.IntegerField(default=10, validators=[MinValueValidator(0), MaxValueValidator(1000)], 
                                     help_text="Stock quantity must be between 0 and 1000.")
    productImage = models.ImageField(upload_to="product_images", blank=True, null=True)

    def __str__(self):
        return self.productName
    
# Cart Model
class Cart(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name="carts")
    created_at = models.DateTimeField(auto_now_add=True)

    def total_cart_price(self):
        return sum(item.sub_total() for item in self.cart_items.all())

    def __str__(self):
        return f"Cart of {self.user.username} - Total Items: {self.cart_items.count()}"
    
# CartItem Model
class CartItem(models.Model):
    cart = models.ForeignKey(Cart, on_delete=models.CASCADE, related_name="cart_items")
    product = models.ForeignKey(Product, on_delete=models.CASCADE)
    qty = models.PositiveIntegerField(default=1, validators=[MinValueValidator(1)])

    def sub_total(self):
        return self.qty * self.product.productPrice
    
    def __str__(self):
        return f"{self.cart.user.username} - {self.product.productName} (x{self.qty})"
    
    # for If you try to add the same product again, Django will raise an IntegrityError.
    class Meta:
        unique_together = ('cart', 'product')

# Order Model 
class Order(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    total_price = models.DecimalField(max_digits=10, decimal_places=2)
    payment_type = models.CharField(max_length=50)
    shipping_address = models.CharField(max_length=255, default="Home") 
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Order {self.id} by {self.user.username}"

    # Method to calculate the total price dynamically based on the order items
    def total_order_price(self):
        return sum(item.sub_total() for item in self.order_items.all())

    @property
    def subtotal(self):
        return sum(item.sub_total() for item in self.order_items.all())  # Dynamic subtotal calculation
    
# OrderItem Model 
class OrderItem(models.Model):
    order = models.ForeignKey(Order, on_delete=models.CASCADE, related_name="order_items")
    product = models.ForeignKey(Product, on_delete=models.CASCADE)
    qty = models.PositiveIntegerField(default=1, validators=[MinValueValidator(1)])

    def sub_total(self):
        return self.qty * self.product.productPrice  # Calculate subtotal dynamically based on product price and quantity

    def __str__(self):
        return f"{self.product.productName} (x{self.qty})" 