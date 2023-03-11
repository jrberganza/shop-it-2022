## PNPM Stage
FROM docker.io/node:16 AS pnpm-base

RUN corepack enable && corepack prepare pnpm@7.29.1 --activate


## Deps Stage
FROM pnpm-base AS deps-base

WORKDIR /app

COPY pnpm-lock.yaml ./

RUN pnpm fetch

COPY package.json ./

RUN pnpm install --frozen-lockfile --offline


## Build Stage
FROM deps-base AS build-base

WORKDIR /app

ENV NODE_ENV production

COPY . .

RUN pnpm run build


## App Stage
FROM docker.io/node:16 AS mycelium-international-client

RUN npm install -g http-server

COPY --from=build-base /app/dist /app
RUN mv /app/index.html /app/404.html

WORKDIR /app

EXPOSE 8080

CMD [ "http-server", "-p", "8080" ]