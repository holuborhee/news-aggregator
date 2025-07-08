FROM node:20-alpine3.20

# Create app directory
WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm install

# Copy app source
COPY . .

# Build the app
RUN npm run build

EXPOSE 3000

CMD ["npm", "run", "start"]
