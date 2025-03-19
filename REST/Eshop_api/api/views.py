from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import IsAuthenticated, IsAdminUser
from django.core.exceptions import ObjectDoesNotExist
from django.shortcuts import render,HttpResponse
from django.shortcuts import get_object_or_404
from rest_framework.response import Response
from rest_framework import status
from api.serializer import *
from api.models import *



# Create your views here.

# < ================================== > CATEGORIES < ==================================================================================
@api_view(['GET'])
def getCategories(request):
    categories = Category.objects.all()
    s_categories = CategorySerializer(categories, many=True)
    
    return Response(data = s_categories.data)

@api_view(['GET'])
def getCategory(request, id):
    category = get_object_or_404(Category, pk=id)
    s_category = CategorySerializer(category)
    return Response(data = s_category.data)

@api_view(['POST'])
def addCategory(request):
    try:
        s_category = CategorySerializer(data=request.data)
        if not s_category.is_valid():
            return Response({'status': '400', 'errors': s_category.errors, 'message': 'Invalid data provided'}, status=status.HTTP_400_BAD_REQUEST)
        s_category.save()
        return Response({'status': '201', 'data': s_category.data, 'message': 'Category Added Successfully'}, status=status.HTTP_201_CREATED)
    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': 'Internal Server Error'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

@api_view(['PATCH'])
@permission_classes([IsAdminUser]) 
def updateCategory(request, id):
    category = get_object_or_404(Category, pk=id)

    serializer = CategorySerializer(category, data=request.data, partial=True)
    if serializer.is_valid():
        serializer.save()
        return Response({'status': '200', 'data': serializer.data, 'message': "Category Updated Successfully" }, status=status.HTTP_200_OK)
    else:
        return Response({ 'status': '400', 'errors': serializer.errors, 'message': 'Invalid data' }, status=status.HTTP_400_BAD_REQUEST)

@api_view(['DELETE'])
@permission_classes([IsAdminUser]) 
def deleteCategory(request, id):
    category = get_object_or_404(Category, pk=id)  # This will raise 404 automatically if category does not exist
    category.delete()
    return Response({'status': '200', 'message': "Category Deleted Successfully"}, status=status.HTTP_200_OK)


# < ================================== > PRODUCTS < ==================================================================================

@api_view(['GET'])
def getProducts(request):
    products = Product.objects.all()
    s_products = ProductSerializer(products, many=True)
    
    return Response(data = s_products.data)

@api_view(['GET'])
def getProduct(request, id):
    product = get_object_or_404(Product, pk=id)
    s_product = ProductSerializer(product)
    return Response(data = s_product.data)
    
@api_view(['GET'])
def getProductsByCategory(request, c_id):
    try:
        products = Product.objects.filter(category_id=c_id)
        s_products = ProductSerializer(products, many=True)
        return Response(s_products.data)
    except Exception as e:
        return Response({'status': '202', 'errors':str(e), 'message': 'Something Went Wrong'})
    
@api_view(['POST'])
def addProduct(request):
    try:
        s_product = ProductSerializer(data=request.data)
        if not s_product.is_valid():
            return Response({'status':'202', 'errors':s_product.errors, 'message':'Something Went Wrong'})
        s_product.save()
        return Response({'status':'200', 'data':s_product.data, 'message':"Product Added Successfully"})
    except Exception as e:
        return Response({'status':'202', 'errors':str(e), 'message':'Something Went Wrong'})

@api_view(['PATCH'])
def updateProduct(request, id):
    try:
        product = Product.objects.get(pk=id)
    except ObjectDoesNotExist:
        return Response({'status': '404', 'message': 'Category not found'}, status=status.HTTP_404_NOT_FOUND)

    serializer = ProductSerializer(product, data=request.data, partial=True)
    if serializer.is_valid():
        serializer.save()
        return Response({'status': '200', 'data': serializer.data, 'message': "Product Updated Successfully" }, status=status.HTTP_200_OK)
    else:
        return Response({ 'status': '400', 'errors': serializer.errors, 'message': 'Invalid data' }, status=status.HTTP_400_BAD_REQUEST)

@api_view(['DELETE'])
def deleteProduct(request, id):
    product = get_object_or_404(Product, pk=id)  
    product.delete()
    return Response({'status': '200', 'message': "Product Deleted Successfully"}, status=status.HTTP_200_OK)


# < ================================== > CART < ==================================================================================

@api_view(['GET'])
@permission_classes([IsAuthenticated])
def getCart(request):
    try:
        cart = get_object_or_404(Cart, user=request.user)
        cart_serializer = CartSerializer(cart)
        return Response({
            'cart': cart_serializer.data
        }, status=status.HTTP_200_OK)
    except Cart.DoesNotExist:
        return Response({'status': '404', 'message': 'Cart not found for this user.'}, status=status.HTTP_404_NOT_FOUND)
    
    
@api_view(['GET'])
@permission_classes([IsAuthenticated])
def getCartItems(request):
    try:
        cart = get_object_or_404(Cart, user=request.user)
        cart_items = CartItem.objects.filter(cart=cart)
        
        if not cart_items.exists():
            return Response({'message': 'Cart is empty'}, status=status.HTTP_200_OK)
        
        cart_item_serializer = CartItemSerializer(cart_items, many=True)
        return Response({'cart_items': cart_item_serializer.data}, status=status.HTTP_200_OK)
    except Cart.DoesNotExist:
        return Response({'status': '404', 'message': 'Cart not found for this user.'}, status=status.HTTP_404_NOT_FOUND)



@api_view(['POST'])
@permission_classes([IsAuthenticated])
def addToCart(request):
    try:
        serializer = AddToCartSerializer(data=request.data)                 # Validate input using the serializer
        
        if serializer.is_valid():
            product_id = serializer.validated_data['product_id']
            quantity = serializer.validated_data['quantity']
            
            product = get_object_or_404(Product, pk=product_id)

            if quantity > product.productQty:                                # Check stock availability
                return Response({"error": "Not enough stock available"}, status=status.HTTP_400_BAD_REQUEST)

            cart, created = Cart.objects.get_or_create(user=request.user)

            cart_item, created = CartItem.objects.get_or_create(cart=cart, product=product)

            if created:
                cart_item.qty = quantity
                cart_item.save()
                return Response({'message': 'Item added to cart successfully', 'data': CartItemSerializer(cart_item).data }, status=status.HTTP_201_CREATED)
            else:
                cart_item.qty += quantity
                cart_item.save()
                return Response({ 'message': 'Product already in cart, quantity updated', 'data': CartItemSerializer(cart_item).data }, status=status.HTTP_200_OK)
        else:
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)

    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': 'Something went wrong'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

@api_view(['PATCH'])
@permission_classes([IsAuthenticated])
def updateCartItem(request, cart_item_id):
    try:
        quantity = request.data.get("quantity")
        
        if quantity is None or quantity < 1:
            return Response({"error": "Quantity must be greater than 0."}, status=status.HTTP_400_BAD_REQUEST)
        
        cart_item = get_object_or_404(CartItem, pk=cart_item_id, cart__user=request.user)
        product = cart_item.product
        
        if quantity > product.productQty:
            return Response({"error": "Not enough stock available"}, status=status.HTTP_400_BAD_REQUEST)
        
        cart_item.qty = quantity
        cart_item.save()

        return Response({'message': 'Cart updated successfully', 'data': CartItemSerializer(cart_item).data}, status=status.HTTP_200_OK)
    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': 'Something went wrong'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

@api_view(['DELETE'])
@permission_classes([IsAuthenticated])
def removeCartItem(request, cart_item_id):
    try:
        cart_item = get_object_or_404(CartItem, pk=cart_item_id, cart__user=request.user)
        cart_item.delete()
        return Response({'message': 'Cart deleted successfully', 'data': CartItemSerializer(cart_item).data}, status=status.HTTP_200_OK)
    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': 'Something went wrong'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

@api_view(['DELETE'])
@permission_classes([IsAuthenticated])
def clearCart(request):
    try:
        cart = get_object_or_404(Cart, user=request.user)
        cart.cart_items.all().delete()                      # kyuki model me related_name diya hai
        return Response({'message': 'Cart cleared successfully'}, status=status.HTTP_200_OK)

    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': 'Something went wrong'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# < ================================== > ORDERS < ==================================================================================


@api_view(['GET'])
@permission_classes([IsAuthenticated])
def getOrders(request):
    orders = Order.objects.filter(user=request.user)
    if not orders.exists():
            return Response({'message': 'No Orders Yet!'}, status=status.HTTP_200_OK)
    s_orders = OrderSerializer(orders, many=True)
    return Response(s_orders.data)


@api_view(['POST'])
@permission_classes([IsAuthenticated])
def createOrder(request):
    try:
        cart = get_object_or_404(Cart, user=request.user)
         
        if cart.cart_items.count() == 0:
             return Response({"message": "Cart is empty"}, status=status.HTTP_400_BAD_REQUEST)
        
        payment_type = request.data.get("payment_type", "Cash On Delivery")
        shipping_address = request.data.get("shipping_address", "Home")
        
        total_price = cart.total_cart_price()
        
        order = Order.objects.create(user=request.user, total_price=total_price, payment_type=payment_type, shipping_address=shipping_address )
        
        for cart_item in cart.cart_items.all():
            OrderItem.objects.create(
                order=order,
                product=cart_item.product,
                qty=cart_item.qty
            )
        
        cart.cart_items.all().delete()
        
        order_serializer = OrderSerializer(order)
        return Response({ "message": "Order placed successfully", "data": order_serializer.data }, status=status.HTTP_201_CREATED)
        
    except Exception as e:
        return Response({"status": "500", "message": "Something went wrong", "errors": str(e)}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)