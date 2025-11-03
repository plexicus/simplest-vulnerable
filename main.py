# main.py
from fastapi import FastAPI, Request, Path
from fastapi.responses import HTMLResponse
import html

app = FastAPI()

@app.get("/{user_input}", response_class=HTMLResponse)
async def read_user_input(user_input: str = Path(..., regex=r"^[A-Za-z0-9_.-]{0,100}$")):
    # Escape user input to prevent XSS and constrain allowed characters via Path regex
    # This avoids reflecting raw HTML back to clients.
    safe_input = html.escape(user_input)
    return f"<html><body><h1>Your input was: {safe_input}</h1></body></html>"
