# main.py
from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse
import html

app = FastAPI()

@app.get("/{user_input:path}", response_class=HTMLResponse)
async def read_user_input(user_input: str):
    # Escape user input to prevent XSS attacks
    sanitized_input = html.escape(user_input)
    return f"<html><body><h1>Your input was: {sanitized_input}</h1></body></html>"


