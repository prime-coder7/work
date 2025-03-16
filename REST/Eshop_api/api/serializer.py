from rest_framework import serializers
from api.models import *

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
    
    
class CartSerializer(serializers.ModelSerializer):
    class Meta:
        model = Cart
        fields = "__all__"
        
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['user'] = CategorySerializer(instance.user).data
        return resp
    
class CartItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = CartItem
        fields = "__all__"
    
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['cart'] = CategorySerializer(instance.cart).data
        resp['product'] = CategorySerializer(instance.product).data
        return resp

class OrderSerializer(serializers.ModelSerializer):
    class Meta:
        model = Order
        fields = "__all__"
        
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['user'] = CategorySerializer(instance.user).data
        return resp

class OrderItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = OrderItem
        fields = "__all__"
    
    def to_representation(self, instance):
        resp = super().to_representation(instance)
        resp['order'] = CategorySerializer(instance.order).data
        resp['product'] = CategorySerializer(instance.product).data
        return resp