"use server";

import { cookies } from "next/headers";

export async function loginUser(formData: { email: string; password: string }) {
  const res = await fetch(`${process.env.API_URL}/login`, {
    method: "POST",
    body: JSON.stringify(formData),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  });

  if (!res.ok) {
    const err = await res.json();
    throw new Error(JSON.stringify(err));
  }

  const data = await res.json();

  const cookieStore = await cookies();
  cookieStore.set("token", data.token);
  cookieStore.set("email", data.user.email);
  cookieStore.set("name", data.user.name);

  return data;
}

export async function registerUser(formData: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}) {
  const res = await fetch(`${process.env.API_URL}/register`, {
    method: "POST",
    body: JSON.stringify(formData),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  });

  if (!res.ok) {
    const err = await res.json();
    throw new Error(JSON.stringify(err));
  }

  const data = await res.json();

  const cookieStore = await cookies();
  cookieStore.set("token", data.token);
  cookieStore.set("email", data.user.email);
  cookieStore.set("name", data.user.name);

  return data;
}

export async function logoutUser() {
  const cookieStore = await cookies();
  const token = cookieStore.get("token")?.value;

  await fetch(`${process.env.API_URL}/logout`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  // Clear cookies
  cookieStore.delete("token");
  cookieStore.delete("email");
  cookieStore.delete("name");
}
