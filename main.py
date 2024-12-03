# main.py
from fastapi import FastAPI, Request
import html
from fastapi.responses import HTMLResponse

app = FastAPI()

@app.get("/{user_input}", response_class=HTMLResponse)
async def read_user_input(user_input: str):
    # Esta línea introduce una vulnerabilidad XSS porque el input del usuario se
    # devuelve directamente en la respuesta sin ninguna sanitización o escape.
    # Un atacante podría ingresar un script como parte de user_input.
    return HTMLResponse(content=f"<html><body><h1>Your input was: {html.escape(user_input)}</h1></body></html>", status_code=200)


