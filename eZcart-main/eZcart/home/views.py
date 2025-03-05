from django.shortcuts import render, redirect
from django.contrib import messages
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.models import User
from django.contrib.auth.decorators import login_required
from home.models import *
from django.http import JsonResponse
from django.conf import settings

# Create your views here. 

def index(request):
    categories = Category.objects.all()
    products = Product.objects.all()
    context = {
        'is_index': True,  # Flag for index page
        'categories': categories,
        'products': products,
    }
    return render(request, "index.html", context)

def shop(request):
    categories = Category.objects.all()
    products = Product.objects.all()
    context = {
        'categories': categories,
        'products': products,
    }
    return render(request, "shop.html", context)

def get_products_by_category(request):
    cid = request.GET.get("cid")
    if cid == "":
        products = Product.objects.all()
    else:
        products = Product.objects.filter(category_id=cid)

    product_list = [
        {
            'productId' : product.id,
            'productName': product.productName,
            'productPrice': product.productPrice,
            'productImage': { 'url': product.productImage.url }
        }
        for product in products
    ]
    return JsonResponse({"cproducts": product_list})

def searchProduct(request):
    val = request.GET.get('val')

    products = Product.objects.filter(productName__istartswith=val)

    product_list = [
        {
            'productId' : product.id,
            'productName': product.productName,
            'productPrice': product.productPrice,
            'productImage': { 'url': product.productImage.url }
        }
        for product in products
    ]
    return JsonResponse({"cproducts": product_list})

def product_detail(request):
    id = request.GET['id']
    pdata = Product.objects.get(pk=id)

    products = Product.objects.all()
    categories = Category.objects.all()

    context = {
        'pdata' : pdata,
        'categories': categories,
        'products': products,
    }
    return render(request, "product-detail.html", context)


def addToCart(request):
    pid = request.GET.get('pid')  
    qty = int(request.GET.get('qty', 1))  # Default to 1 if qty is not provided
    user_id = request.user.id if request.user.is_authenticated else None
    
    if not pid:
        return JsonResponse({"error": "Product ID is required"}, status=400)

    print(pid, user_id, qty)
    try:
        user = request.user 
        product = Product.objects.get(pk=pid)
        
        # Get the user's cart, or create a new one if it doesn't exist
        cart, created = Cart.objects.get_or_create(user=user)
        
        # Check if the product is already in the cart
        cart_item = CartItem.objects.filter(cart=cart, product=product).first()
        
        if cart_item:
            # Update the quantity if the product is already in the cart
            cart_item.qty += qty
            cart_item.save()
        else:
            # Create a new cart item if not already in the cart
            CartItem.objects.create(cart=cart, product=product, qty=qty)
            
        return JsonResponse({"success": True, "message": "Product added to cart"})
    
    except Product.DoesNotExist:
        return JsonResponse({"error": "Product not found"}, status=404)

@login_required(login_url="login_user")
def shoping_cart(request):
    # Fetch the user's cart
    user = request.user
    cart, created = Cart.objects.get_or_create(user=user)

    # Get all cart items for this cart
    cart_items = CartItem.objects.filter(cart=cart)

    # Calculate subtotal, shipping (you can update this based on your logic)
    subtotal = sum(item.total_price() for item in cart_items)

    context = {
        'cart_items': cart_items,
        'subtotal': subtotal,
    }

    return render(request, "shoping-cart.html", context)

def whishlist(request):
    return render(request, "whishlist.html")

def features(request):
    return render(request, "features.html")

def blog(request):
    return render(request, "blog.html")

def blog_detail(request):
    return render(request, "blog-detail.html")

def about(request):
    return render(request, "about.html")

def contact(request):
    return render(request, "contact.html")

@login_required(login_url="login_user")
def checkout(request):
    return render(request, "checkout.html")

def login_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    
    if request.method == "POST":
        data = request.POST
        username = data.get('loginusername')
        password = data.get('loginpassword')

        if not username or not password:
            messages.error(request, "Enter All Details !!!")
        else:
            user = authenticate(username=username, password=password)
            if user:
                login(request, user)
                messages.success(request, "Login Successfull !!!")
                return redirect("index")
            else:
                messages.error(request, "Enter Correct Details !!!")
                return render(request, "login.html")
    return render(request, "login.html")

def signup_user(request):
    # if request.user.is_authenticated:
    #     return redirect("index")
    
    pattern = "^[a-zA-Z0-9]+@[a-zA-Z]+.[a-zA-Z]{2,4}$"
    
    if request.method == "POST":
        data = request.POST
        username = data.get('signupusername')
        email = data.get('signupemail')
        password = data.get('signuppassword')
        cpassword = data.get('confirmpassword')

        if not username or not email or not password or not cpassword:
            messages.error(request, "Enter All Details !!!")
        else:
            if User.objects.filter(username=username).exists():
                messages.error(request, "User Already exist !!!")
            else:
                if password == cpassword:
                    user = User(username=username, email=email)
                    user.set_password(password)
                    user.save()
                    messages.success(request, "Registration Successfull !!!")
                    return redirect("login_user")
                else:
                    messages.error(request, "Passwords do not match. Please ensure that both passwords are same.")
    return render(request, "login.html")

def logout_user(request):
    if request.user.is_anonymous:
        return redirect("login_user")
    
    if request.user.is_authenticated:
        logout(request)
        messages.success(request, "Logout Successfull !!!")
    return render(request, "login.html")

def help(request):
    return render(request, "help.html")

@login_required(login_url="login_user")
def profile(request):
    if request.user.is_anonymous:
        return redirect("login_user")
    return render(request, "profile.html")

def my_orders(request):
    context = {
        # orders = Order.objects.filter(user=request.user).order_by('-created_at')
    }
    return render(request, 'my_orders.html', context)

def address_book(request):
    context = {
        # addresses = Address.objects.filter(user=request.user)
    }
    return render(request, 'address_book.html', context)

def acc_setting(request):
    return render(request, "acc_setting.html")

def my_wishlist(request):
    context = {
        # wishlist_items = Wishlist.objects.filter(user=request.user)
    } 
    return render(request, 'my_wishlist.html', context)




# extra:
def home2(request):
    return render(request, "home-02.html")

def home3(request):
    return render(request, "home-03.html")

