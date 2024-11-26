import httpx

url = "http://localhost:8000/<script>alert('XSS')</script>"  # Example of malicious input

response = httpx.get(url)

print(response.text)  # Print the response to see if the input was sanitized
