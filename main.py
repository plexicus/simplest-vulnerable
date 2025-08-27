# main.py
from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse

app = FastAPI()

@app.get("/{user_input}", response_class=HTMLResponse)
from fastapi.templating import Jinja2Templates

templates = Jinja2Templates(directory="templates")

@app.get("/{user_input}", response_class=HTMLResponse)
async def read_user_input(user_input: str):
    # This fix mitigates XSS by using a templating engine with autoescaping
    return templates.TemplateResponse("response.html", {"request": None, "user_input": user_input})


