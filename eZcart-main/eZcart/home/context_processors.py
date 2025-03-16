from .models import Cart, CartItem

def cart_count(request):
    cart_count = 0
    if request.user.is_authenticated:
        try:
            cart = Cart.objects.get(user=request.user)
            # Sum the quantity of all items in the cart
            cart_count = sum(item.qty for item in cart.cart_items.all())
        except Cart.DoesNotExist:
            cart_count = 0
    return {'cart_count': cart_count}
