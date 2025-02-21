import requests

def get_random_joke():
    url = "https://official-joke-api.appspot.com/random_joke"
    
    try:
        # Send GET request to the API
        response = requests.get(url)
        
        # Check if the request was successful
        if response.status_code == 200:
            joke = response.json()
            print(f"{joke['setup']} - {joke['punchline']}")
        else:
            print(f"Failed to fetch joke. Status code: {response.status_code}")
    
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    get_random_joke()
