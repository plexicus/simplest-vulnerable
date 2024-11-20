from fastapi.testclient import TestClient
from main import app

client = TestClient(app)

def test_xss_protection():
    response = client.get("/<script>alert('XSS')</script>")
    assert response.status_code == 200
    assert "<script>alert('XSS')</script>" not in response.text
    assert "&lt;script&gt;alert(&#x27;XSS&#x27;)&lt;/script&gt;" in response.text

print("Test completed successfully, no errors.")
