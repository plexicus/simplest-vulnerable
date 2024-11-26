 # main.py
 from fastapi import FastAPI, Request
 from fastapi.responses import HTMLResponse
import html
 
 app = FastAPI()
 
@app.get("/{user_input}", response_class=HTMLResponse)
     # Esta línea introduce una vulnerabilidad XSS porque el input del usuario se
     # devuelve directamente en la respuesta sin ninguna sanitización o escape.
     # Un atacante podría ingresar un script como parte de user_input.
    sanitized_input = html.escape(user_input)  # Sanitize user input
    return f"<html><body><h1>Your input was: {sanitized_input}</h1></body></html>"
 
 

