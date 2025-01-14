import html
from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse

app = FastAPI()

@app.get("/{user_input}", response_class=HTMLResponse)
async def read_user_input(user_input: str):
    return f"<html><body><h1>Your input was: {html.escape(user_input)}</h1></body></html>"
# Removed duplicate import statements and app initialization


