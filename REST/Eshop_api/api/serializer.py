from rest_framework import serializers
from api.models import *

class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = ['id', 'username', 'email']

class CategorySerializer(serializers.ModelSerializer):
    class Meta:
        model = Category
        fields = "__all__"

class ProductSerializer(serializers.ModelSerializer):
    class Meta:
        model = Product
        fields = "__all__"
    
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['category'] = CategorySerializer(instance.category).data
        return resp
    

class AddToCartSerializer(serializers.Serializer):
    product_id = serializers.IntegerField()
    quantity = serializers.IntegerField(default=1)  
    
class CartSerializer(serializers.ModelSerializer):
    class Meta:
        model = Cart
        fields = "__all__"
        
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['user'] = UserSerializer(instance.user).data
        return resp
    
class CartItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = CartItem
        fields = "__all__"
    
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['cart'] = CartSerializer(instance.cart).data
        resp['product'] = ProductSerializer(instance.product).data
        return resp

class OrderItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = OrderItem
        fields = "__all__"
    
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['order'] = OrderSerializer(instance.order).data
        resp['product'] = ProductSerializer(instance.product).data
        return resp

class OrderSerializer(serializers.ModelSerializer):
    order_items = OrderItemSerializer(many=True)
    total_price = serializers.SerializerMethodField()

    class Meta:
        model = Order
        fields = "__all__"

    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['user'] = UserSerializer(instance.user).data
        return resp

    def get_total_price(self, obj):
        # This calls the total_order_price method of the Order model to calculate total price
        return obj.total_order_price()